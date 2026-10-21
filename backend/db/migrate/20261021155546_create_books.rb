class CreateBooks < ActiveRecord::Migration[8.1]
  def change
    create_table :books do |t|
      t.string :isbn
      t.string :title
      t.string :subtitle
      t.text :content
      t.string :contributor
      t.string :imprint
      t.string :publisher
      t.string :picture
      t.integer :price
      t.date :date
      t.integer :audience_type
      t.integer :audience_code
      t.string :c_code
      t.string :subject_text
      t.string :isbn_10
      t.string :amazon_url
      t.string :honto_url
      t.string :book_type

      t.timestamps
    end
  end
end
