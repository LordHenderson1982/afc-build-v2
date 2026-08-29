import { useState } from 'react'
import { useNavigate } from 'react-router-dom'

export default function Home() {
  const [search, setSearch] = useState('')
  const navigate = useNavigate()

  const handleSearch = (e) => {
    e.preventDefault()
    if (search.trim()) {
      navigate(`/search?q=${encodeURIComponent(search)}`)
    }
  }

  return (
    <div>
      <div className="hero">
        <h1>Find & Book Bowling Lanes</h1>
        <p>Discover bowling alleys near you. Book your lane in seconds.</p>
        
        <form onSubmit={handleSearch} className="search-box">
          <input 
            type="text" 
            placeholder="Enter city or zip code..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
          />
          <button type="submit">Search</button>
        </form>
      </div>
      
      <div className="features">
        <div className="feature">
          <div className="feature-icon">🔍</div>
          <h3>Find Alleys</h3>
          <p>Search thousands of bowling alleys across the USA</p>
        </div>
        
        <div className="feature">
          <div className="feature-icon">📅</div>
          <h3>Book Lanes</h3>
          <p>Reserve your lane in advance</p>
        </div>
        
        <div className="feature">
          <div className="feature-icon">⭐</div>
          <h3>Save Favorites</h3>
          <p>Keep track of your favorite alleys</p>
        </div>
      </div>
    </div>
  )
}
