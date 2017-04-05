class Legend extends React.Component {

  changeHandler(e) {
    if (typeof this.props.changeType === 'function') {
      this.props.changeType(e);
    }
  }

  pad(n) {
    return (n < 10) ? ("0" + n) : n;
  }


  prevMonth(){
    this.props.prevMonth();
  }

  nextMonth(){
    this.props.nextMonth();
  }

  setDate(e){
    this.props.setDate(e.target.value);
  }

  render(){
    if(this.props.edit){
      var items = [];
      for(let i in this.props.items){
        let item = JSON.parse(JSON.stringify(this.props.items[i]));
        if(this.props.editType != item.id_type){
          item.color = "white";
        }
        if(item.id_type === 0){
          item.type = 'DELETE';
        }
        items.push( <div type="button" key={item.id_type} style={{'backgroundColor':item.color}} onClick={()=>{this.changeHandler(item.id_type)}} className="btn">{item.type}</div>);
      }
    }

    let classname = 'btn' + (this.props.edit ? ' btn-danger' : ' btn-success');
    let name = this.props.edit ? ' save':'edit';
    let date =  this.props.date.getFullYear() + '-' + this.pad(this.props.date.getMonth() + 1);
    return(
      <div>
        <div className="btn btn-default" onClick={()=>{this.prevMonth()}}>&lt;</div>
        <input type="month" value={date} onChange={(e)=>{this.setDate(e)}} />
        <div className="btn btn-default" onClick={()=>{this.nextMonth()}}>&gt;</div>
        <div className={classname}  onClick={this.props.toggle}>{name}</div>
        {items}
      </div>
    )
  }

}

class FormRow extends React.Component {
  getDaysInMonth(date) {
    var month = date.getMonth()
    var date = new Date(date.getFullYear(), month, 1);
    var days = [];
    while (date.getMonth() === month) {
      days.push(new Date(date));
      date.setDate(date.getDate() + 1);
    }
    return days;
  }


  pad(n) {
    return (n < 10) ? ("0" + n) : n;
  }


  handleClick(user,date){
    let data = {
      user:JSON.parse(JSON.stringify(user)),
      date: date
    }
    this.props.handleUpdate(data)
  }
  handleSelectUser(id){
    this.props.handleSelectUser(id);
  }


  render (){
    var cells = [];
    var days = this.getDaysInMonth(this.props.date);

    for(var i in days ){
      let date = days[i].getFullYear() + '-' + this.pad(days[i].getMonth() + 1) + '-' + this.pad(days[i].getDate());
      let data = this.props.data;


      if(data === undefined || data[date] === undefined ) {
        cells.push(<td style={{'backgroundColor':'white'}} key={i} onClick={() => {this.handleClick(this.props.user,date)}}></td>) ;
        continue;
      }

      let type = parseInt(data[date]);
      let color = JSON.parse(JSON.stringify(this.props.items[type].color));
      let text = JSON.parse(JSON.stringify(this.props.items[type].type));

      cells.push(<td style={{'backgroundColor':color}} key={i} onClick={() => {this.handleClick(this.props.user,date)}}>{text}</td>) ;
    }

    return(
      <tr>
        <td onClick={() => {this.handleSelectUser(this.props.user.id_employee)}} key="32">{this.props.user.meno}</td>
        {cells}
      </tr>
    )

  }
}


class Form extends React.Component {
  getDaysInMonth(date) {
    var month = date.getMonth()
    var date = new Date(date.getFullYear(), month, 1);
    var days = [];
    while (date.getMonth() === month) {
      days.push(new Date(date));
      date.setDate(date.getDate() + 1);
    }
    return days;
  }

  handleUpdate(e){
    this.props.handleUpdate(e);
  }
  handleSelectUser(e){
    this.props.handleSelectUser(e);
  }

  render() {
    var days = this.getDaysInMonth(this.props.date);
    var users = this.props.users;
    var thead = [];
    for(var i in days ){
      var day = days[i];

      let color = '#ddd';
      if(day.getDay() === 0 ||day.getDay() === 6  ){
        color = '#0f0';
      }
      let dow = day.toDateString().substr(0,3)
      let text =  day.getDate();
      thead.push(<th key={i} style={{'backgroundColor':color}}>{dow}<br/>{text}</th>);

    }

    var tbody = [];
    for(var j in users){
      if(this.props.user !== 0 && this.props.user !== parseInt(users[j].id_employee)){
        continue;
      }
      let user = JSON.parse(JSON.stringify(users[parseInt(j)]));
      let data
      if(typeof(this.props.data[user.id_employee]) !== 'undefined'){
         data = JSON.parse(JSON.stringify(this.props.data[user.id_employee]));
      }else{
         data = {};
      }
      tbody.push(<FormRow
        key={j}
        user={user}
        data={data}
        edit={this.props.edit}
        date={this.props.date}
        handleUpdate={(e)=>{this.handleUpdate(e)}}
        items={this.props.items}
        handleSelectUser={(e)=>{this.handleSelectUser(e)}}/>);
    }
    let moths = {
      0: 'Januar',
      1: 'Ferbuar',
      2: 'Marec',
      3: 'April',
      4: 'Maj',
      5: 'Jun',
      6: 'Jul',
      7: 'August',
      8: 'September',
      9: 'Oktober',
      10: 'November',
      11: 'December'
    };
    let month = moths[this.props.date.getMonth() ];



    return (
      <div id="datepicker">
        <table className="table-bordered table-striped">
          <thead>
          <tr>
            <th onClick={()=>{this.handleSelectUser(0)}}>{month}</th>
            {thead}
          </tr>
          </thead>
          <tbody>
          {tbody}
          </tbody>
        </table>
      </div>

    );
  }
}

class App extends React.Component {
  constructor(props) {
    var state = {date: new Date(), legend:[], users:[], data:[], edit:false, editType:1, user:0};
    super(props);
    this.state = state;
  }

  loadData (jdata) {
    var data = new FormData();
    data.append( "json", JSON.stringify( jdata ) );
    data.append( "date", this.state.date.toDateString()  );
    fetch("api/?request=getState",{
      method: "POST",
      body: data
    })
      .then(function(response) {
        return response.json()
          .then(function(json){
            var state = this.state;
            state.users = json.data.users;
            state.data = json.data.data;
            state.legend = json.data.legend;
            state.legend.unshift({
              id_type:0,
              type:'',
              color:'white'
            })
            this.setState(state);
          }.bind(this))
      }.bind(this));

  }

  toggleEdit (){
    var state = this.state;
    if(state.edit){
      this.loadData(this.state.data);
    }
    state.edit = !state.edit;
    this.setState(state);
  }

  changeType(type){
    var state = this.state;
    state.editType = parseInt(type);
    this.setState(state);
  }

  componentDidMount() {
    this.loadData();
  }

  handleUpdate(e){
    if(!this.state.edit){
      return;
    }
    let user = e.user.id_employee;
    let date = e.date;
    let state = this.state;
    let data = state.data;


    if(data[user] === undefined){
      data[user] = {};
    }

    if(data[user][date] === undefined){
      data[user][date] = {};
    }

    data[user][date] = this.state.editType;

    state.data = data;
    this.setState(state);
  }

  prevMonth(){
    let state = this.state;
    state.date.setMonth(this.state.date.getMonth() -1 );
    this.setState(state);
  }

  nextMonth(){
    let state = this.state;
    state.date.setMonth(this.state.date.getMonth() + 1 );
    this.setState(state);
  }

  setDate(date){
    let state = this.state;
    date = date.split('-');
    state.date.setMonth(date[1] - 1 );
    state.date.setYear(date[0] );
    this.setState(state);
  }

  handleSelectUser(e) {
    let state = this.state;
    state.user = parseInt(e);
    this.setState(state);
  }


  render() {
    return (
      <div className="">
        <Legend items={this.state.legend}
                toggle={() => {this.toggleEdit()}}
                changeType={(e) => {this.changeType(e)}}
                edit={this.state.edit}
                date={this.state.date}
                editType={this.state.editType}
                nextMonth={() => {this.nextMonth()}}
                prevMonth={() => {this.prevMonth()}}
                setDate={(e) => {this.setDate(e)}}/>
        <Form users={this.state.users}
              data={this.state.data}
              date={this.state.date}
              handleUpdate={(e)=>{this.handleUpdate(e)}}
              items={this.state.legend}
              handleSelectUser = {(e)=>{this.handleSelectUser(e)}}
              user={this.state.user}/>
      </div>

    );
  }
}


ReactDOM.render(
  React.createElement(App),
  document.getElementById('root')
);