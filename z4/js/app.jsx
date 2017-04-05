
var Table = Reactable.Table,
  Tr = Reactable.Tr,
  unsafe = Reactable.unsafe;

class App extends React.Component {
  constructor(props) {
    var state = {
      dept:0,
      data:false
    };
    super(props);
    this.state = state;
  }

  componentWillMount(){
    this.fetchFreeProjects(642);
  }


  fetchFreeProjects(newDept){
    fetch("api/?request=getFreeProjects&project=" + newDept,{
      method: "GET",
    })
      .then(function(response) {
        return response.json()
          .then(function(json){
            let state  = this.state;
            state.projects = json.data;
            this.setState(state);
          }.bind(this))
      }.bind(this));
  }


  selectDept(e){
    let state = this.state;
    let newDept = parseInt(e.target.value);
    console.log(newDept);
    if(state.dept !== newDept){
      this.fetchFreeProjects(newDept);
      state.dept = newDept;
      this.setState(state);
    }
  }

  showItem(link){



  }


  render() {
    const ustavy = [
      {name:"Ústav automobilovej mechatroniky",id:"642"},
      {name:"Ústav elektroenergetiky a aplikovanej elektrotechniky",id:"548"},
      {name:"Ústav elektroniky a fotoniky",id:"549"},
      {name:"Ústav elektrotechniky",id:"550"},
      {name:"Ústav informatiky a matematiky",id:"816"},
      {name:"Ústav jadrového a fyzikálneho inžinierstva",id:"817"},
      {name:"Ústav multimediálnych informačných a komunikačných technológií",id:"818"},
      {name:"Ústav robotiky a kybernetiky",id:"356"}];

    let items = [];
    for(let i in ustavy){
      let val = ustavy[i];
      items.push(<option type="button" key={val.id} value={val.id}>{val.name}</option>)
    }


    let projects = this.state.projects;
    for(let i in projects){
      let link  = projects[i].link;
      projects[i].name = <a onClick={(link) => {this.showItem(link)}}>{projects[i].name}</a>;
    }
    console.log(this.state.projects);

    return (
      <div className="">
        <select onChange={(e)=>{this.selectDept(e)}}>{items}</select>
        <div className="data"></div>
        <Table className="table table-striped table-bordered"
          data={projects}
               sortable={true}
               filterable={['name','lead']}
        >
        </Table>
      </div>

    );
  }
}


ReactDOM.render(
  React.createElement(App),
  document.getElementById('root')
);


