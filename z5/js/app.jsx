
class App extends React.Component {
  constructor(props) {
    var state = {
      country:"SK",
      name:"juraj",
      date:'0424',
      type:'sviatky',
      date2:"",
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

    return (
      <div>
        <div>
          <h1>Namesday</h1>
          <formgroup>
            <select value={this.state.country} onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <input type="text" value={this.state.name} onChange={(e)=>this.setVal(e,'name')}/>
            <button className="btn" onClick={()=>this.fetchByName()} >Search</button>
          </formgroup><br/>
          <formgroup>
            <select value={this.state.country} onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <input type="text" value={this.state.date} onChange={(e)=>this.setVal(e,'date')}/>
            <button className="btn" onClick={()=>this.fetchByDate()} >Search</button>
          </formgroup>
          <h1>Holidays</h1>
          <formgroup>
            <select value={this.state.country} onChange={(e)=>{this.setVal(e,'country')}}>{items}</select>
            <select value={this.state.type} onChange={(e)=>{this.setVal(e,'type')}}>{itemsTypes}</select>
            <input type="text" value={this.state.date2} onChange={(e)=>this.setVal(e,'date2')}/>
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
