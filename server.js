import express from 'express'
import http from 'http'
import { Server } from 'socket.io'

const app = express()
const server = http.createServer(app)
const io = new Server(server, {
  allowEIO3: true,
  cors: {
    origin: 'http://localhost:8000',
    methods: ['GET', 'POST'],
    credentials: true,
  },
  transports: ['polling', 'websocket']
})

io.on('connection', (socket, next) => {
  const username  = socket.handshake.auth?.username
  console.log(`${username} connected to socket`);

  if (!username) return next(new Error('invalid username'));

  socket.on('disconnect', () => {
    console.log("a user disconnected", socket);
  })

  next();
})

server.listen(3000, () => {
  console.log('Server socket listening on port 3000')
})
