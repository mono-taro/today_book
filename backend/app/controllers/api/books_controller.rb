class Api::BooksController < ApplicationController
  # Base filter for adult content (audience_type=22 AND audience_code IN (01, 02, 03))
  def base_query
    Book.where.not(audience_type: 22, audience_code: [1, 2, 3])
  end

  def today
    books = base_query.where(book_type: 'today')
    render json: books
  end

  def soon
    books = base_query.where(book_type: 'soon')
    render json: books
  end

  def search
    query = base_query

    # Handle adult content exclusion (which is already in base_query, same as PHP code)

    # Title/Subtitle search
    if params[:title].present?
      title_terms = params[:title].gsub('　', ' ').split(' ').compact_blank
      if title_terms.any?
        sql_conds = title_terms.map { |term| Book.sanitize_sql_array(["(title LIKE ? OR subtitle LIKE ?)", "%#{term}%", "%#{term}%"]) }.join(" OR ")
        query = query.where("(#{sql_conds})")
      end
    end

    # Content search
    if params[:content].present?
      content_terms = params[:content].gsub('　', ' ').split(' ').compact_blank
      if content_terms.any?
        sql_conds = content_terms.map { |term| Book.sanitize_sql_array(["content LIKE ?", "%#{term}%"]) }.join(" OR ")
        query = query.where("(#{sql_conds})")
      end
    end

    # Contributor search
    if params[:contributor].present?
      contributor_terms = params[:contributor].gsub('　', ' ').split(' ').compact_blank
      if contributor_terms.any?
        sql_conds = contributor_terms.map { |term| Book.sanitize_sql_array(["contributor LIKE ?", "%#{term}%"]) }.join(" OR ")
        query = query.where("(#{sql_conds})")
      end
    end

    # Imprint search
    if params[:imprint].present?
      imprint_terms = params[:imprint].gsub('　', ' ').split(' ').compact_blank
      if imprint_terms.any?
        sql_conds = imprint_terms.map { |term| Book.sanitize_sql_array(["imprint LIKE ?", "%#{term}%"]) }.join(" OR ")
        query = query.where("(#{sql_conds})")
      end
    end

    # Date search
    if params[:date].present?
      date_terms = params[:date].gsub('/', '').split(' ').compact_blank
      if date_terms.any?
        sql_conds = date_terms.map { |term| Book.sanitize_sql_array(["strftime('%Y%m%d', date) = ?", term]) }.join(" OR ")
        query = query.where("(#{sql_conds})")
      end
    end

    # C-code search
    c_code = "#{params[:C_code01]}#{params[:C_code02]}#{params[:C_code03]}"
    if c_code.present?
      if params[:check] == 'true'
        query = query.where("c_code LIKE ? OR c_code = ?", "#{c_code}%", "____")
      else
        query = query.where("c_code LIKE ?", "#{c_code}%")
      end
    end

    render json: query.limit(200)
  end
end
