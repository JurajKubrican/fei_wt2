class App extends React.Component {
  constructor(props) {
    var state = {
      country:"SK",
      name:"juraj",
      date:'0424',
      type:'sviatky',
      date2:"",
      data:{},
    };
    super(props);
    this.state = state;
  }

  fetchByName(){
    let request = 'api/stat/' + this.state.country + '/meno/' + this.state.name;
    this.fetch(request,'GET');
  }

  fetchByDate(){
    let date = this.state.date;
    console.log(date);
    let request = 'api/stat/' + this.state.country + '/meniny/' + date;
    this.fetch(request,'GET');

  }

  fetchByType(){
    let request = 'api/stat/' + this.state.country + '/' + this.state.type +'/' + this.state.date2;
    this.fetch(request,'GET');
  }



  fetch(url,method,data){
    let state  = this.state;
    state.request = url;
    state.status = 'pending';
      this.setState(state);


    fetch(url,{
      method: method,
      body:JSON.stringify(data)
    })
      .then(function(response) {
        console.log(response);
        return response.json()
          .then(function(json){
            let state  = this.state;
            state.data = json.data;
            this.setState(state);
          }.bind(this))
      }.bind(this));
  }

  setVal(e,name){
    let state = this.state;
    let val = e.target.value;
    state[name] = val;
    this.setState(state);
  }

  setEditableVal(e,i,j){
    let state = this.state;
    let val = e.target.value;
    state.data[i][j] = val;
    this.setState(state);
  }

  saveEditable(i){

    let request = 'api/stat/' + this.state.country + '/meniny/' + this.state.data[i].den;
    this.fetch(request,'PUT',this.state.data[i]);

  }



  render() {

    const countries = [
      {name:"Slovensko",id:"SK"},
      {name:"Ceska republika",id:"CZ"},
      {name:"Rakusko",id:"AT"},
      {name:"Polsko",id:"PL"},
      {name:"Madarsko",id:"HU"},
    ];

    let items = [];
    for(let i in countries){
      let val = countries[i];
      items.push(<option key={val.id} value={val.id}>{val.name}</option>)
    }

    const types = [
      {name:"Sviatky",id:"sviatky"},
      {name:"Dni",id:"dni"},
    ];

    let itemsTypes = [];
    for(let i in types){
      let val = types[i];
      itemsTypes.push(<option key={val.id} value={val.id}>{val.name}</option>)
    }


    let editable = [];
    for(let i in this.state.data){
      let itemsEditable=[];
      for(let j in this.state.data[i]){
        let val = this.state.data[i][j];
        // if(typeof(val) === 'object'){
        //   val = val.join(', ');
        // }
        itemsEditable.push(<tr key={j}><td key="1">{j}</td><td key="2"><input className="form-control" type="text" value={val} onChange={(e)=>this.setEditableVal(e,i,j)}/></td></tr>);
      }


      editable.push(<table key={i}>
        <tbody>
        {itemsEditable}
        <tr key="submit"><td key="1"></td><td key="2"><button onClick={(e)=>this.saveEditable(i)}>submit</button></td></tr>
        </tbody>
      </table>)
    }



    return (
      <div>
        <div className="form-inline">
          <h1>Namesday</h1>
          <formgroup>
            <select className="form-control " value={this.state.country} onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <input className="form-control" type="text" value={this.state.name} onChange={(e)=>this.setVal(e,'name')}/>
            <button className="btn" onClick={()=>this.fetchByName()} >Search</button>
          </formgroup><br/>
          <formgroup>
            <select className="form-control" value={this.state.country} onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <input className="form-control" type="text" value={this.state.date} onChange={(e)=>this.setVal(e,'date')}/>
            <button className="btn" onClick={()=>this.fetchByDate()} >Search</button>
          </formgroup>
          <h1>Holidays</h1>
          <formgroup>
            <select className="form-control" value={this.state.country} onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <select className="form-control" value={this.state.type} onChange={(e)=>{this.setVal(e,'type')}}>{itemsTypes}</select>
            <input className="form-control" type="text" value={this.state.date2} onChange={(e)=>this.setVal(e,'date2')}/>
            <button className="form-control btn" onClick={()=>this.fetchByType()} >Search</button>
          </formgroup>
          {editable}
        </div>
      </div>

    );
  }
}


ReactDOM.render(
  React.createElement(App),
  document.getElementById('root')
);
