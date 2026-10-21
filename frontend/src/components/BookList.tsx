import { useState, useEffect } from 'react'
import type { Book } from '../types'
import BookItem from './BookItem'
import { Alert, Spinner } from 'react-bootstrap'

interface Props {
  type: 'today' | 'soon'
}

export default function BookList({ type }: Props) {
  const [books, setBooks] = useState<Book[]>([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    setLoading(true)
    fetch(`/api/books/${type}`)
      .then(res => res.json())
      .then(data => {
        setBooks(data)
        setLoading(false)
      })
      .catch(err => {
        console.error(err)
        setLoading(false)
      })
  }, [type])

  if (loading) {
    return <Spinner animation="border" />
  }

  return (
    <div>
      {books.length === 0 ? (
        <Alert variant="danger">出版予定の本はありません。</Alert>
      ) : (
        <Alert variant="success">出版予定の本は{books.length}件あります。</Alert>
      )}

      {books.map(book => (
        <BookItem key={book.id} book={book} />
      ))}
    </div>
  )
}
