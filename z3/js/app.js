/**
 * Created by kubri on 18.3.2017.
 */
('use strict');
$(document).ready(function(){

  let drawing = false;
  let last = {x:0,y:0};
  let canvas = $('#drawPlane');
  let ctx = canvas[0].getContext('2d')
  let lastEmit = $.now();

  let socket = new WebSocket('wss://wt.knet.sk:5500');

  canvas.bind('mousedown',function(e){
    e.preventDefault();
    last = {
      x:e.offsetX,
      y:e.offsetY
    }
    drawing = true;
  }).bind('mouseup',function(){
    drawing = false;
  }).bind('mousemove',function(e){
    if(!drawing)
      return;

    let curr = {
      x:e.offsetX,
      y:e.offsetY
    }



    if($.now() - lastEmit > 10){
      drawLine(last,curr,color);
      socket.send(JSON.stringify({from:last,to:curr,col:color}));
      lastEmit = $.now();
      last = curr;
    }


  })

  function drawLine(from, to, col){
    ctx.beginPath();
    ctx.strokeStyle=col;
    ctx.lineWidth = 50;
    ctx.moveTo(from.x, from.y);
    ctx.lineTo(to.x, to.y);
    ctx.stroke();
    ctx.closePath();

  }

  socket.onmessage =function(message){
    console.log(message.data);
    message = $.parseJSON(message.data);
    drawLine(message.from,message.to,message.col);

  }



  $('#save').click(function(e){
    console.log(e.target)
    e.target.href = document.getElementById('drawPlane').toDataURL();
    e.target.download = filename;
  });



});