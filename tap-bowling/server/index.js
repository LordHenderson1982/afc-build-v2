import express from 'express'
import cors from 'cors'
import dotenv from 'dotenv'

dotenv.config()

const app = express()
const PORT = process.env.PORT || 5001

app.use(cors())
app.use(express.json())

// Routes will go here
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok', message: 'TAP Bowling API is running' })
})

app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`)
})
