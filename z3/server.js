var server = require('websocket').server,
  https = require('https'),
  fs = require('fs');

var socket = new server({
    httpServer: https.createServer({
      key: fs.readFileSync('privkey.pem'),
      cert: fs.readFileSync('cert.pem'),
      NPNProtocols: ['http/2.0', 'spdy', 'http/1.1', 'http/1.0']
    }).listen(5500)
});

console.log('started');
var counter = 0;
var clients = [];

socket.on('request', function(request) {
    var connection = request.accept(null, request.origin);
    clients[counter] = connection;
    connection.id = counter;
    counter++;

    connection.on('message', function(message) {
        console.log(message.utf8Data);

        for (index in clients){
            if(clients[index].id != connection.id){
                clients[index].send(message.utf8Data);
            }
        }
    });

    connection.on('close', function(connection) {
        delete clients[connection.id];
    });
}); 