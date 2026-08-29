import { useState, useEffect } from 'react'
import { Link, useSearchParams } from 'react-router-dom'
import axios from 'axios'

export default function Search() {
  const [searchParams, setSearchParams] = useSearchParams()
  const [query, setQuery] = useState(searchParams.get('q') || '')
  const [alleys, setAlleys] = useState([])
  const [loading, setLoading] = useState(false)

  const handleSearch = (e) => {
    e.preventDefault()
    setSearchParams({ q: query })
  }

  useEffect(() => {
    const q = searchParams.get('q')
    if (q) {
      setQuery(q)
      searchAlleys(q)
    }
  }, [searchParams])

  const searchAlleys = async (searchQuery) => {
    setLoading(true)
    try {
      const res = await axios.get(`/api/alleys/search?q=${encodeURIComponent(searchQuery)}`)
      setAlleys(res.data)
    } catch (err) {
      console.error('Search error:', err)
      // For now, show mock data
      setAlleys([
        { id: 1, name: 'AMF Bowling', address: '123 Main St, New York, NY', city: 'New York', state: 'NY', phone: '(212) 555-0100', lanes: 24 },
        { id: 2, name: 'Bowling World', address: '456 Oak Ave, Los Angeles, CA', city: 'Los Angeles', state: 'CA', phone: '(213) 555-0200', lanes: 32 },
        { id: 3, name: 'Strike Zone', address: '789 Pine Rd, Chicago, IL', city: 'Chicago', state: 'IL', phone: '(312) 555-0300', lanes: 18 },
      ])
    }
    setLoading(false)
  }

  return (
    <div className="search-page">
      <form onSubmit={handleSearch} className="search-filters">
        <input 
          type="text" 
          placeholder="City, state, or zip code..."
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          style={{ flex: 1 }}
        />
        <button type="submit" style={{ padding: '0.75rem 1.5rem', background: '#1a365d', color: 'white', border: 'none', borderRadius: '8px', cursor: 'pointer' }}>
          Search
        </button>
      </form>
      
      {loading ? (
        <p>Searching...</p>
      ) : alleys.length > 0 ? (
        <div className="results">
          {alleys.map(alley => (
            <Link key={alley.id} to={`/alley/${alley.id}`} className="alley-card">
              <div className="alley-image">🎳</div>
              <div className="alley-info">
                <div className="alley-name">{alley.name}</div>
                <div className="alley-address">{alley.address}</div>
                <div className="alley-details">
                  <span>{alley.city}, {alley.state}</span>
                  <span>{alley.lanes} lanes</span>
                  <span>{alley.phone}</span>
                </div>
              </div>
            </Link>
          ))}
        </div>
      ) : query ? (
        <p>No alleys found. Try a different search.</p>
      ) : (
        <p>Enter a city or zip code to find bowling alleys.</p>
      )}
    </div>
  )
}
