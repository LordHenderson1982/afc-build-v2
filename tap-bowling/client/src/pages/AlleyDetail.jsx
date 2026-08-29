import { useState, useEffect } from 'react'
import { useParams, Link } from 'react-router-dom'
import axios from 'axios'

export default function AlleyDetail() {
  const { id } = useParams()
  const [alley, setAlley] = useState(null)
  const [loading, setLoading] = useState(true)
  const [booking, setBooking] = useState({
    name: '',
    email: '',
    phone: '',
    date: '',
    time: '',
    lanes: 1,
    notes: ''
  })
  const [submitted, setSubmitted] = useState(false)

  useEffect(() => {
    // Mock data for now
    setAlley({
      id,
      name: 'AMF Bowling Center',
      address: '123 Main Street',
      city: 'New York',
      state: 'NY',
      zip: '10001',
      phone: '(212) 555-0100',
      website: 'https://amf.com',
      lanes: 24,
      hours: 'Mon-Fri: 4PM-12AM, Sat-Sun: 10AM-12AM',
      price: '$6/game, $25/shoe rental'
    })
    setLoading(false)
  }, [id])

  const handleSubmit = async (e) => {
    e.preventDefault()
    // In production, this would call the API
    console.log('Booking:', { ...booking, alleyId: id })
    setSubmitted(true)
  }

  if (loading) return <p>Loading...</p>
  if (!alley) return <p>Alley not found</p>

  return (
    <div className="alley-detail">
      <Link to="/search" className="back-link">← Back to Search</Link>
      
      <div className="alley-header">
        <h1>{alley.name}</h1>
        <p className="alley-address-full">{alley.address}, {alley.city}, {alley.state} {alley.zip}</p>
      </div>
      
      <div className="alley-info-grid">
        <div className="info-card">
          <h3>Phone</h3>
          <p>{alley.phone}</p>
        </div>
        <div className="info-card">
          <h3>Website</h3>
          <p><a href={alley.website} target="_blank" rel="noopener">Visit Website</a></p>
        </div>
        <div className="info-card">
          <h3>Lanes</h3>
          <p>{alley.lanes}</p>
        </div>
        <div className="info-card">
          <h3>Hours</h3>
          <p>{alley.hours}</p>
        </div>
        <div className="info-card">
          <h3>Pricing</h3>
          <p>{alley.price}</p>
        </div>
      </div>
      
      <div className="booking-form">
        <h2>Request a Lane</h2>
        {submitted ? (
          <div style={{ padding: '2rem', textAlign: 'center', background: '#c6f6d5', borderRadius: '8px' }}>
            <h3 style={{ color: '#22543d' }}>Request Submitted!</h3>
            <p>The bowling alley will contact you to confirm your booking.</p>
          </div>
        ) : (
          <form onSubmit={handleSubmit}>
            <div className="form-group">
              <label>Your Name</label>
              <input 
                type="text" 
                required
                value={booking.name}
                onChange={(e) => setBooking({...booking, name: e.target.value})}
              />
            </div>
            <div className="form-group">
              <label>Email</label>
              <input 
                type="email" 
                required
                value={booking.email}
                onChange={(e) => setBooking({...booking, email: e.target.value})}
              />
            </div>
            <div className="form-group">
              <label>Phone</label>
              <input 
                type="tel" 
                required
                value={booking.phone}
                onChange={(e) => setBooking({...booking, phone: e.target.value})}
              />
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
              <div className="form-group">
                <label>Date</label>
                <input 
                  type="date" 
                  required
                  value={booking.date}
                  onChange={(e) => setBooking({...booking, date: e.target.value})}
                />
              </div>
              <div className="form-group">
                <label>Time</label>
                <input 
                  type="time" 
                  required
                  value={booking.time}
                  onChange={(e) => setBooking({...booking, time: e.target.value})}
                />
              </div>
            </div>
            <div className="form-group">
              <label>Number of Lanes</label>
              <input 
                type="number" 
                min="1" 
                max="10"
                value={booking.lanes}
                onChange={(e) => setBooking({...booking, lanes: e.target.value})}
              />
            </div>
            <div className="form-group">
              <label>Notes</label>
              <textarea 
                rows="3"
                value={booking.notes}
                onChange={(e) => setBooking({...booking, notes: e.target.value})}
              />
            </div>
            <button type="submit">Submit Request</button>
          </form>
        )}
      </div>
    </div>
  )
}
