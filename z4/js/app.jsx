
var Table = Reactable.Table,
  Tr = Reactable.Tr,
  unsafe = Reactable.unsafe;

class App extends React.Component {
  constructor(props) {
    var state = {
      dept:642,
      view:'projects',
      projects:[],
      focusedProjectData : false,

      userName:'',
      userData:[],
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
    if(state.dept !== newDept){
      this.fetchFreeProjects(newDept);
      state.dept = newDept;
      this.setState(state);
    }
  }

  showItem(link){
    fetch("api/?request=getFocusedProject&project=" + link + '&dept=' + this.state.dept + '',{
      method: "GET",
    })
      .then(function(response) {
        return response.json()
          .then(function(json){
            let state  = this.state;
            state.focusedProjectData = json.data;
            state.view = 'projectsFocused',
            this.setState(state);
          }.bind(this))
      }.bind(this));

  }

  navHome(){
    let state  = this.state;
    state.view = 'projects';
    this.setState(state);
  }

  setView(view){
    let state  = this.state;
    state.view = view;
    this.setState(state);
  }

  setUserName(e){
    let state  = this.state;
    state.userName = e.target.value;
    this.setState(state);
  }

  fetchUser(){
    fetch("api/?request=getProjectsForPerson&person=" + this.state.userName,{
      method: "GET",
    })
      .then(function(response) {
        return response.json()
          .then(function(json){
            let state  = this.state;
            state.userData = json.data;
            //state.view = 'projectsFocused',
              this.setState(state);
          }.bind(this))
      }.bind(this));
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
      projects[i].name = <a onClick={() => {this.showItem(link)}}>{projects[i].name}</a>;
    }

    let viewData = '';
    switch(this.state.view) {
      case "projects" :
        viewData = (
          <div>
            <select onChange={(e)=>{this.selectDept(e)}}>{items}</select>
            <Table className="table table-striped table-bordered"
                   data={projects}
                   sortable={true}
                   filterable={['name','lead']}
            ></Table>
          </div>);
      break;
      case'projectsFocused':
        viewData=(
          <div>
            <button className="btn-danger btn" onClick={()=>{this.navHome()}}>Back</button>
            <Table className="table table-striped table-bordered"
                   data={this.state.focusedProjectData} ></Table>
          </div>);
        break

      case 'users':
        viewData=(
          <div>
            <input type="text" value={this.state.userName} onChange={(e)=>this.setUserName(e)}/>
            <button className="btn" onClick={()=>this.fetchUser()} >Search</button>
            <Table className="table table-striped table-bordered"
                   data={this.state.focusedProjectData} ></Table>
          </div>);
        break;
    }


    return (
      <div>
        <button className="btn" onClick={()=>this.setView('projects')}>Projects</button>
        <button className="btn" onClick={()=>this.setView('users')}>Users</button>
        {viewData}
      </div>

    );
  }
}


ReactDOM.render(
  React.createElement(App),
  document.getElementById('root')
);


