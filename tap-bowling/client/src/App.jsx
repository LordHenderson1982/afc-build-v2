import { useState, useEffect } from 'react'
import { BrowserRouter, Routes, Route, Link } from 'react-router-dom'
import Home from './pages/Home'
import Search from './pages/Search'
import AlleyDetail from './pages/AlleyDetail'

function App() {
  return (
    <BrowserRouter>
      <div className="app">
        <nav className="navbar">
          <div className="nav-container">
            <Link to="/" className="logo">🎳 TAP Bowling</Link>
            <div className="nav-links">
              <Link to="/search">Find Alleys</Link>
              <Link to="/about">About</Link>
            </div>
          </div>
        </nav>
        
        <main className="main-content">
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/search" element={<Search />} />
            <Route path="/alley/:id" element={<AlleyDetail />} />
          </Routes>
        </main>
        
        <footer className="footer">
          <p>© 2026 TAP Bowling - Your game, elevated.</p>
        </footer>
      </div>
    </BrowserRouter>
  )
}

export default App
