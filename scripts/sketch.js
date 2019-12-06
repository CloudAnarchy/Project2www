
function setup() {
    
    is_id_set();

    // let cnv = createCanvas(1550, 1536);
    let cnv = createCanvas(1500, 300);
    background(0, 10, 10);
    cnv.parent('sketch-holder'); 

    setupEventHandlers();
    cnv.mousePressed(clickCell);
    DATA.setCanvas(cnv);
}

async function is_id_set(){
    //console.log(window.location.href); 
    let arr = window.location.href.split("index.php?id=");

    let num;
    if (!isNaN(num = parseInt(arr[1]))) {
        console.log(num);
        // Getting info from the files
        fetch(`http://localhost/testing/passData.php?id=${num}`)
        .then(result => {

            return receiveData(result.json());
        })
        .catch(error => console.log(error));
    }
}

function createBoard(board){

    let cnv = createCanvas(board.width * 1, board.height * 1);
    cnv.parent('sketch-holder');
    background(0, 10, 10);

    // Makes white lines
    stroke(25);
    let i ;
    for (i = 0; i < height; i += 16) line(0, i, width, i);
}


async function receiveData(data) {
    //console.log(data);
    console.log("I am fetching the design...");
    Utils.makeCells(await data);
}


function setupEventHandlers(){

    
    // Input fields
    // Nets
    $('#pick-net').bind('keyup', function(e){

        if (e.keyCode === 13) {
            e.preventDefault();
            if (this.value >= 0 && this.value < DATA.numNets) {
                //console.log(this.value);
                UIcontroll.showPickedNet(this.value * 1);
            }
        }
    });
    // Lines
    $('#pick-line').bind('keyup', function(e){
        if(e.keyCode === 13){
            e.preventDefault();
            //                This will return the number of lines we have
            if (this.value >= 0 && this.value <= DATA.board.height / 16)
                UIcontroll.showCellsInSameLine(this.value * 16);
        }
    });
    // Nodes
    $('#pick-node').bind('keyup', function(e){
        if(e.keyCode === 13){
            e.preventDefault();
            Utils.findNode(this.value);
        }
    });
    // Reset Buttons
    $('#reset-nets-colors').bind('click', colorReset);
    $('#reset-lines-colors').bind('click', colorReset);
    $('#reset-nodes-colors').bind('click', colorReset);

    // For showing border cells
    $('#border-cells').bind('click', UIcontroll.showBorderCells);

    $('#screenshot').bind('click', function(e){
        saveCanvas(DATA.canvas, 'myDesign', 'jpg');
    });

    $('#statistics').bind('click', Utils.createStats);

}

function colorReset(){
    stroke('red');
    fill(20);
    DATA.cellsArr.forEach(cell => {
        cell.reColor();
    });
}

function draw() {
    
}

function clickCell(){

    let xMouse = Math.round(mouseX);
    let yMouse = Math.round(mouseY);
   
    // Find the closest Y row to the mouseClick
    // method deconstructing
    let [rowArr, cellClicked]  = Utils.findLine(yMouse);


    // Find the cell closest to the mouseClick    
    let difX = Infinity;
    rowArr.forEach(cell => {
        if(Math.abs(cell.x - xMouse) < difX){
            difX = Math.abs(cell.x - xMouse)
            cellClicked = cell;
        }
    });
    ////////////////////////////////


    // Show it in the UI
    UIcontroll.colorNode(cellClicked);



}

function test() {

}

async function makeNets(){

    UIcontroll.showNetsInLabel();
    UIcontroll.showLinesInLabel();
    UIcontroll.showNodesInLabel();
    // Each net gets assigned a unique color!
    let arr = [];
    for (let i = 0; i <= DATA.numNets; i++) arr.push(new Net(i));
    DATA.setNets(arr);
}

// To write less code (and for practice) I will be using the selector from jQuery
// e.x: $('#net-note').[0] === document.getElementById('net-note')
//      $('.myClass') will return every el with that has as a class myClass.
const UIcontroll = {

    showNetsInLabel: () => {

        let num = DATA.numNets;
        $('#net-note')[0].innerHTML = `<strong>NOTE</strong> total nets in this design: (${num})`; 
    },
    showLinesInLabel: () => {

        let num = DATA.board.height / 16;
        $('#lines-note')[0].innerHTML = `<strong>NOTE</strong> total nets in this design: (${num})`;
    },
    showNodesInLabel: () => {

        let num = DATA.numNodes;
        $('#nodes-note')[0].innerHTML = `<strong>NOTE</strong> total nodes in this design: (${num})`;
    },
    showCellsInSameLine: y => {
        
        const [rowArr, cellAtYlvl] = Utils.findLine(y);
        fill(Utils.colors[3]);
        console.log(rowArr);
        rowArr.forEach(cell => cell.reColor());
    },

    colorNode: cell => {
        fill(Utils.colors[5]);
        cell.reColor();

        $('#cell-clicked')[0].innerHTML = `<span>name:</span> ${cell.name}<br><span>x:</span> ${cell.x}<br><span>y:</span> ${cell.y}<br><span>nets:</span> ${cell.nets}`;
    },
    showBorderCells: function () {

        let count = 0;
        let arr = [];
        let height = DATA.board.height - 16;
        let width = DATA.board.width * 1;

        fill(Utils.colors[6]);
        DATA.cellsArr.forEach(cell => {
            // 
            if (cell.x === 0 || cell.y === 0 || cell.y === height || (cell.x + cell.width) >= width) {
                count++;
                cell.reColor();
                arr.push(cell);
            }

        });
        let el = $('#reveal-border-cell-info')[0];
        el.innerHTML  =`There are <span><strong>${count}</strong></span> border cells`;
        el.style.marginTop = '10px';
        el.visibility = 'visible';

        // console.log(`There are ${count} border cells`);
        // console.log(arr);
    },
    
    showPickedNet: id => {
        fill(DATA.netsArr[id].getColor());

        DATA.cellsArr.forEach(cell => {
            // If the cell contains the specific net (in his nets array) we asked for then it will return true
            isThereThatNet = cell.getNets().find(net => net === id);
            if(isThereThatNet){
                //console.log(cell);
                cell.reColor();
            }
        });
    }
}




const DATA = {
    setCanvas: function (cnv) {
        this.canvas = cnv;
    },
    setData  : function(board, numNets){
        this.board    = board;
        this.numRows  = board.width / 16;
        this.numNets  = numNets;
    },
    setCells : function(cellsArr){
        this.cellsArr = cellsArr;
        this.numNodes = this.cellsArr.length;
    },
    setNets  : function(netsArr){
        this.netsArr  = netsArr;
    }
}


const Utils = {

    colors: ['#532dbc', '#23a870', '#f450bb', '#f45050', '#f4a250', '#0ec6ef', '#d8d81a'],

    rndNum: num => {
        return Math.round(Math.random() * num);
    },


    // We use function() here instead of => because with arrow functions
    // the this keyword uses the surrouding object which in this case is Window
    // this means when i use this. arrCells it created an attribute ti Window object
    // indead of the Utils object.
    makeCells: async function(data){

        let count = 0;
        let arr = [];

        // We remove the first element of the data array with is the object of the board from php
        // The sceond element is the number of nets we have
        DATA.setData(data.shift(), data.shift());
        
        createBoard(DATA.board);
        makeNets(DATA.numNets);

        // Makes stroke atribute red
        // fill changes the color as well. 20 is a shade of grey
        // 0 is black 255 is white. 
        stroke('red');
        fill(20);
        data.forEach(cell => {
            arr[count++] = new Cell(cell.name, cell.x, cell.y, cell.width, cell.height, cell.nets, cell.isTerminal);
        });

        DATA.setCells(arr);
        
    },
    
    createStats: () => {
        let nNodes        = DATA.numNodes;
        let nNets         = DATA.numNets;
        let nRows         = DATA.numRows;
        let nTerminals    = 0;
        let nNonTerminals = 0;
        DATA.cellsArr.forEach(cell => cell.isTerminal ? nTerminals++ : nNonTerminals++);

        let netsArr = DATA.netsArr;
        
        DATA.cellsArr.forEach(cell => {  
            cell.getNets().forEach(net => netsArr[net].setCell(cell));
        });
        
        // Find the net with the most Nodes.
        let max = netsArr[0];
        netsArr.forEach(net => max.getArrCells().length < net.getArrCells().length ? max = net : max);
        console.log(max);

    },

    findLine: (y) => {

        // Find the cells at the same Y
        let dif = Infinity;
        let cellAtYlvl; // This var is only used later on when we call it from showCellClicked()
        

        // Find that line Y
        DATA.cellsArr.forEach(cell => {
            if (Math.abs(cell.y - y) < dif) {
                dif = Math.abs(cell.y - y)
                cellAtYlvl = cell;
            }
        });

        // Filter and get the line of cells we want
        let difArr = DATA.cellsArr.filter(cell =>  cell.y === cellAtYlvl.y);

        return [difArr, cellAtYlvl];
        ////////////////////////////////
    },

    findNode: (cell_entered) => {
        let cell = DATA.cellsArr.find(tempCell => tempCell.getName() === cell_entered);
        console.log(cell);
        UIcontroll.colorNode(cell);
    },
    
};