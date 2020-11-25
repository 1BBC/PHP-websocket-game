const unit = document.getElementById('unit');
unit.style.backgroundColor = '#' + (Math.random().toString(16) + '000000').substring(2,8).toUpperCase();
let params = (new URL(document.location)).searchParams;
let room_id = params.get('room_id') ?? 1

const ws = new WebSocket('ws://172.20.0.91:7777?room_id=' + room_id);

document.addEventListener('keyup',  function(event) {
    let top = unit.style.top ? unit.style.top : 0;
    let left = unit.style.left ? unit.style.left : 0;
    const step = 5;

    if (event.code === 'ArrowUp') {
        unit.style.top = parseInt(top) - step + 'px';
    } else if (event.code === 'ArrowDown') {
        unit.style.top = parseInt(top) + step + 'px';
    } else if (event.code === 'ArrowLeft') {
        unit.style.left = parseInt(left) - step + 'px';
    } else if (event.code === 'ArrowRight') {
        unit.style.left = parseInt(left) + step + 'px';
    }

    let positionData = {
        top: unit.style.top,
        left: unit.style.left,
        room_id: params.get('room_id'),
    };

    ws.send(JSON.stringify(positionData));
})

ws.onmessage = response => {
    let positionData = JSON.parse(response.data);
    console.log(positionData);
    unit.style.top = positionData.top;
    unit.style.left = positionData.left;
}