require 'rss'
require 'open-uri'
require 'json'

namespace :books do
  desc "Fetch today's and tomorrow's books from Hanmoto and OpenBD"
  task fetch: :environment do
    fetch_books_for("https://www.hanmoto.com/ci/bd/search/sdate/day/edate/day/hdt/%E6%9C%AC%E6%97%A5%E7%99%BA%E5%A3%B2%E3%81%AE%E6%9C%AC/order/desc/vw/rss20", "today")
    fetch_books_for("https://www.hanmoto.com/ci/bd/search/sdate/day/edate/day/hdt/%E6%98%8E%E6%97%A5%E7%99%BA%E5%A3%B2%E3%81%AE%E6%9C%AC/order/desc/vw/rss20", "soon")
  end

  def fetch_books_for(rss_url, book_type)
    puts "Fetching #{book_type} books..."

    # In a real app we'd clear the old ones or just update. The PHP script was doing TRUNCATE.
    # Let's delete existing books of this type to simulate TRUNCATE.
    Book.where(book_type: book_type).destroy_all

    URI.open(rss_url) do |rss|
      feed = RSS::Parser.parse(rss)
      feed.items.each do |item|
        link = item.link
        # Extract ISBN13 from URL path, e.g., https://www.hanmoto.com/bd/isbn/9784798167069 -> 9784798167069
        isbn13 = URI.parse(link).path.split('/').last

        next unless isbn13 && isbn13.length == 13

        # Check digit calc for ISBN 10
        isbn13_chars = isbn13.chars
        sum = 0
        (3..11).each do |i|
          sum += (13 - i) * isbn13_chars[i].to_i
        end
        checkdigit_10 = 11 - (sum % 11)
        checkdigit_10 = 'X' if checkdigit_10 == 10
        checkdigit_10 = '0' if checkdigit_10 == 11

        isbn10_result = isbn13[3..11] + checkdigit_10.to_s
        amazon_url = "https://www.amazon.co.jp/dp/#{isbn10_result}"
        honto_url = "http://honto.jp/redirect.html?bookno=#{isbn13[0..-2]}"

        openbd_url = "https://api.openbd.jp/v1/get?isbn=#{isbn10_result}"

        begin
          json_data = URI.open(openbd_url).read
          arr = JSON.parse(json_data)

          next if arr.nil? || arr.empty? || arr[0].nil?

          onix = arr[0]["onix"]
          next if onix.nil?

          book_isbn = onix.dig("RecordReference")
          book_title = onix.dig("DescriptiveDetail", "TitleDetail", "TitleElement", "TitleText", "content")
          next if book_title.nil?

          book_subtitle = onix.dig("DescriptiveDetail", "TitleDetail", "TitleElement", "Subtitle", "content")

          contributors = onix.dig("DescriptiveDetail", "Contributor")
          book_contributor = contributors&.first&.dig("PersonName", "content")

          book_content = nil
          text_contents = onix.dig("CollateralDetail", "TextContent")
          if text_contents.is_a?(Array)
            text_contents.reverse.each_with_index do |tc, idx|
              if tc["Text"] && !tc["Text"].empty?
                # A simplified version of their logic
                prev_text = idx < text_contents.size - 1 ? text_contents.reverse[idx + 1]["Text"] : ""
                book_content = prev_text.to_s + "<br><br>" + tc["Text"].to_s + "<br>"
                break
              end
            end
          end

          book_imprint = onix.dig("PublishingDetail", "Imprint", "ImprintName")
          book_publisher = onix.dig("PublishingDetail", "Publisher", "PublisherName")

          resources = onix.dig("CollateralDetail", "SupportingResource")
          book_picture = resources&.first&.dig("ResourceVersion")&.first&.dig("ResourceLink")
          book_picture = "no_image.png" if book_picture.nil? || book_picture.empty?

          prices = onix.dig("ProductSupply", "SupplyDetail", "Price")
          book_price = prices&.first&.dig("PriceAmount")

          dates = onix.dig("PublishingDetail", "PublishingDate")
          book_date_str = dates&.first&.dig("Date")
          book_date = Date.parse(book_date_str) rescue nil

          audiences = onix.dig("DescriptiveDetail", "Audience")
          audience_type = audiences&.first&.dig("AudienceCodeType") || 99
          audience_code = audiences&.first&.dig("AudienceCodeValue") || 99

          c_code = "____"
          subject_text = nil
          subjects = onix.dig("DescriptiveDetail", "Subject")
          if subjects.is_a?(Array)
            subjects.reverse.each do |sub|
              c_code = sub["SubjectCode"] if sub["SubjectSchemeIdentifier"] == "78"
              subject_text = sub["SubjectHeadingText"] if sub["SubjectSchemeIdentifier"] == "20"
            end
          end

          Book.create!(
            isbn: book_isbn,
            title: book_title,
            subtitle: book_subtitle,
            content: book_content,
            contributor: book_contributor,
            imprint: book_imprint,
            publisher: book_publisher,
            picture: book_picture,
            price: book_price,
            date: book_date,
            audience_type: audience_type.to_i,
            audience_code: audience_code.to_i,
            c_code: c_code,
            subject_text: subject_text,
            isbn_10: isbn10_result,
            amazon_url: amazon_url,
            honto_url: honto_url,
            book_type: book_type
          )

        rescue => e
          puts "Error processing #{isbn10_result}: #{e.message}"
        end
      end
    end
    puts "Finished fetching #{book_type} books."
  end
end
