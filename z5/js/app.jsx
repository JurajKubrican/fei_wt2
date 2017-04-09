
class App extends React.Component {
  constructor(props) {
    var state = {
      country:"SK",
      name:"",
      date:'',
    };
    super(props);
    this.state = state;
  }

  fetchByName(){
    let country = this.state.country === 'all' ? '' : ('&country=' + this.state.country)
    let request = 'api/namesday?name=' + this.state.name;
    this.fetch(request,'GET');
  }

  fetchByDate(){
    let country = this.state.country === 'all' ? '' : ('&country=' + this.state.country)
    let request = 'api/namesday?date=' + this.state.date;
    this.fetch(request,'GET');

  }

  fetchByType(){
    let country = this.state.country === 'all' ? '' : ('&country=' + this.state.country)
    let request = 'api/namesday?date=' + this.state.date;
    this.fetch(request,'GET');
  }



  fetch(url,method){
    let state  = this.state;
    state.request = url;
    state.status = 'pending',
    this.setState(state);


    fetch(url,{
      method: method,
    })
      .then(function(response) {
        console.log(response);
        return response.json()
          .then(function(json){
            let state  = this.state;
            state.focusedProjectData = json.data;
            state.view = 'projectsFocused',
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



  render() {

    const countries = [
      {name:"all",id:"ALL"},
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
      {name:"all",id:"all"},
      {name:"Sviatky",id:"sviatky"},
      {name:"Dni",id:"dni"},
    ];

    let itemsTypes = [];
    for(let i in types){
      let val = types[i];
      itemsTypes.push(<option key={val.id} value={val.id}>{val.name}</option>)
    }

    return (
      <div>
        <div>
          <h1>Namesday</h1>
          <formgroup>
            <select onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <input type="text" value={this.state.name} onChange={(e)=>this.setVal(e,'name')}/>
            <button className="btn" onClick={()=>this.fetchByName()} >Search</button>
          </formgroup><br/>
          <formgroup>
            <select onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <input type="date" value={this.state.date} onChange={(e)=>this.setVal(e,'date')}/>
            <button className="btn" onClick={()=>this.fetchByDate()} >Search</button>
          </formgroup>
          <h1>Holidays</h1>
          <formgroup>
            <select onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <select onChange={(e)=>{this.setVal(e,'type')}}>{itemsTypes}</select>
            <button className="btn" onClick={()=>this.fetchByType()} >Search</button>
          </formgroup>
        </div>
      </div>

    );
  }
}


ReactDOM.render(
  React.createElement(App),
  document.getElementById('root')
);
