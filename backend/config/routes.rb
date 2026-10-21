Rails.application.routes.draw do
  namespace :api do
    get 'books/today', to: 'books#today'
    get 'books/soon', to: 'books#soon'
    get 'books/search', to: 'books#search'
  end
end
