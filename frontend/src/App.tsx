import { Routes, Route, Link } from 'react-router-dom'
import { Navbar, Nav, Container } from 'react-bootstrap'
import BookList from './components/BookList'
import SearchForm from './components/SearchForm'

function App() {
  return (
    <>
      <Navbar bg="dark" variant="dark" expand="lg">
        <Container>
          <Navbar.Brand as={Link} to="/">本日の本</Navbar.Brand>
          <Navbar.Toggle aria-controls="basic-navbar-nav" />
          <Navbar.Collapse id="basic-navbar-nav">
            <Nav className="me-auto">
              <Nav.Link as={Link} to="/soon">明日の本</Nav.Link>
              <Nav.Link as={Link} to="/search">本を探す</Nav.Link>
            </Nav>
          </Navbar.Collapse>
        </Container>
      </Navbar>

      <Container className="mt-4">
        <Routes>
          <Route path="/" element={<BookList type="today" />} />
          <Route path="/soon" element={<BookList type="soon" />} />
          <Route path="/search" element={<SearchForm />} />
        </Routes>
      </Container>
    </>
  )
}

export default App
