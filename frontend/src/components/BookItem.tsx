import type { Book } from '../types'
import { Card, Button, Row, Col } from 'react-bootstrap'
import DOMPurify from 'dompurify'

interface Props {
  book: Book
}

export default function BookItem({ book }: Props) {
  let imprintStr = book.imprint ? `発行元出版社:${book.imprint}` : ''
  let publisherStr = book.publisher ? ` / 販売元出版社:${book.publisher}` : ''
  let priceStr = book.price ? ` / 価格:${book.price}円` : ''

  let dateStr = ''
  if (book.date) {
    const d = new Date(book.date)
    dateStr = ` / 出版日:${d.getFullYear()}年${d.getMonth() + 1}月${d.getDate()}日`
  }

  return (
    <Card className="mb-3">
      <Card.Body>
        <Row>
          <Col xs={12} lg={3} className="text-center text-lg-start mb-3 mb-lg-0">
            <img
              src={book.picture === 'no_image.png' ? '/no_image.png' : book.picture}
              alt={book.title}
              style={{ width: '100%', maxWidth: '200px' }}
              className="img-fluid"
            />
          </Col>
          <Col xs={12} lg={9}>
            <Card.Title>{book.title}</Card.Title>
            <Card.Subtitle className="mb-2 text-muted">
              {book.contributor} ISBN:{book.isbn}
            </Card.Subtitle>
            {book.content && (
              <Card.Text dangerouslySetInnerHTML={{ __html: DOMPurify.sanitize(book.content) }} />
            )}
            <Card.Text>
              {imprintStr}{publisherStr}{priceStr}{dateStr}
            </Card.Text>
            <div className="d-flex gap-2">
              {book.amazon_url && (
                <Button variant="primary" href={book.amazon_url} target="_blank">
                  Amazon
                </Button>
              )}
              {book.honto_url && (
                <Button variant="primary" href={book.honto_url} target="_blank">
                  honto
                </Button>
              )}
            </div>
          </Col>
        </Row>
      </Card.Body>
    </Card>
  )
}
