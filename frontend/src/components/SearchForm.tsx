import { useState } from 'react'
import { Form, Button, Alert, Spinner } from 'react-bootstrap'
import { Book } from '../types'
import BookItem from './BookItem'

export default function SearchForm() {
  const [results, setResults] = useState<Book[]>([])
  const [loading, setLoading] = useState(false)
  const [searched, setSearched] = useState(false)

  const [formData, setFormData] = useState({
    title: '',
    contributor: '',
    imprint: '',
    content: '',
    date: '',
    C_code01: '_',
    C_code02: '_',
    C_code03: '__',
    check: false,
    radio1: 'AND',
    radio2: 'AND',
    radio3: 'AND',
    radio4: 'AND',
    radio5: 'AND'
  })

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value, type } = e.target
    const checked = (e.target as HTMLInputElement).checked
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }))
  }

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setSearched(true)

    const params = new URLSearchParams()
    Object.entries(formData).forEach(([key, value]) => {
      params.append(key, value.toString())
    })

    fetch(`/api/books/search?${params.toString()}`)
      .then(res => res.json())
      .then(data => {
        setResults(data)
        setLoading(false)
      })
      .catch(err => {
        console.error(err)
        setLoading(false)
      })
  }

  return (
    <div>
      <Form onSubmit={handleSubmit} className="mb-4">
        <Form.Group className="mb-3">
          <Form.Label>タイトル・サブタイトル</Form.Label>
          <Form.Control type="text" name="title" value={formData.title} onChange={handleChange} placeholder="空白区切りでOR検索" />
        </Form.Group>

        <Form.Group className="mb-3">
          <Form.Label>内容</Form.Label>
          <Form.Control type="text" name="content" value={formData.content} onChange={handleChange} placeholder="空白区切りでOR検索" />
        </Form.Group>

        <Form.Group className="mb-3">
          <Form.Label>著者</Form.Label>
          <Form.Control type="text" name="contributor" value={formData.contributor} onChange={handleChange} placeholder="空白区切りでOR検索" />
        </Form.Group>

        <Form.Group className="mb-3">
          <Form.Label>出版社</Form.Label>
          <Form.Control type="text" name="imprint" value={formData.imprint} onChange={handleChange} placeholder="空白区切りでOR検索" />
        </Form.Group>

        <Form.Group className="mb-3">
          <Form.Label>出版日</Form.Label>
          <Form.Control type="text" name="date" value={formData.date} onChange={handleChange} placeholder="20201021 空白区切りでOR検索" />
        </Form.Group>

        <Form.Group className="mb-3">
          <Form.Label>C-code</Form.Label>
          <div className="d-flex flex-wrap gap-3">
            <div>
              <Form.Label className="small text-muted mb-1">読者対象</Form.Label>
              <Form.Select name="C_code01" value={formData.C_code01} onChange={handleChange}>
                  <option value="_">選択しない</option>
                  <option value="0">一般</option>
                  <option value="1">教養</option>
                  <option value="2">実用</option>
                  <option value="3">専門</option>
                  <option value="4">検定教科書等</option>
                  <option value="5">婦人</option>
                  <option value="6">学参Ⅰ(小中)</option>
                  <option value="7">学参Ⅱ(高校)</option>
                  <option value="8">児童</option>
              </Form.Select>
            </div>
            <div>
              <Form.Label className="small text-muted mb-1">形態</Form.Label>
              <Form.Select name="C_code02" value={formData.C_code02} onChange={handleChange}>
                  <option value="_">選択しない</option>
                  <option value="0">単行本</option>
                  <option value="1">文庫</option>
                  <option value="2">新書</option>
                  <option value="3">全集・双書</option>
                  <option value="4">ムック・その他</option>
                  <option value="5">事・事典</option>
                  <option value="6">図鑑</option>
                  <option value="7">絵本</option>
                  <option value="8">磁気媒体等</option>
              </Form.Select>
            </div>
            <div>
              <Form.Label className="small text-muted mb-1">内容</Form.Label>
              <Form.Select name="C_code03" value={formData.C_code03} onChange={handleChange}>
                  <option value="__">選択しない</option>
                  <option value="0_">(総記)</option>
                  <option value="00">総記</option>
                  <option value="01">百科事典</option>
                  <option value="02">年鑑・雑誌</option>
                  <option value="04">情報科学</option>
                  <option value="1_">(哲学・宗教・心理学)</option>
                  <option value="10">哲学</option>
                  <option value="11">心理(学)</option>
                  <option value="12">倫理(学)</option>
              </Form.Select>
            </div>
          </div>
          <Form.Check
            type="checkbox"
            label="未分類含める"
            name="check"
            checked={formData.check}
            onChange={handleChange}
            className="mt-3"
          />
        </Form.Group>

        <Button variant="dark" type="submit">
          検索
        </Button>
      </Form>

      {loading && <Spinner animation="border" />}

      {!loading && searched && (
        <>
          {results.length === 0 ? (
            <Alert variant="danger">検索対象は見つかりませんでした。</Alert>
          ) : (
            <Alert variant="success">
              {results.length}件見つかりました。
              {results.length >= 200 && <><br/>200件のみ表示します。</>}
            </Alert>
          )}

          {results.map(book => (
            <BookItem key={book.id} book={book} />
          ))}
        </>
      )}
    </div>
  )
}
