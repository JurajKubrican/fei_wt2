
var Table = Reactable.Table,
  Tr = Reactable.Tr,
  unsafe = Reactable.unsafe;


class App extends React.Component {
  constructor(props) {
    var state = {
      view:'weather',
      data:[],
    };
    super(props);
    this.state = state;
  }

  componentWillMount(){
    this.fetch(this.state.view);
  }


  fetch(view){
    fetch("api/?request=" + view  ,{
      method: "GET",
    })
      .then(function(response) {
        return response.json()
          .then(function(json){
            let state  = this.state;
            state.data = json.data;
            state.view = view;
            this.setState(state);
          }.bind(this))
      }.bind(this));
  }

  showItem(item,e){
    console.log(item,e);
    fetch("api/?request=statsCountry&country=" + item  ,{
      method: "GET",
    })
      .then(function(response) {
        return response.json()
          .then(function(json){
            let state  = this.state;
            state.data = json.data;
            this.setState(state);
          }.bind(this))
      }.bind(this));

  }




  render() {

    let data = this.state.data;
    for(let i in data){
      let link  = data[i];
      if(link.filter){
        delete(link.filter);
        let key = link.key;
        data[i].key = <a onClick={() => {this.showItem(key)}}>{key}</a>;
      }


    }

    if(this.state.view === 'stats'){
      $('#map').css('visibility','visible');
    }else{
      $('#map').css('visibility','hidden');
    }


    return (
      <div>
        <button className="btn" onClick={()=>this.fetch('weather')}>weather</button>
        <button className="btn" onClick={()=>this.fetch('geoip')}>geoip</button>
        <button className="btn" onClick={()=>this.fetch('stats')}>stats</button>
        <Table className="table table-striped table-bordered"
               data={data}/>
      </div>

    );
  }
}


ReactDOM.render(
  React.createElement(App),
  document.getElementById('root')
);

