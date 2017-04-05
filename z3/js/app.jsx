class App extends React.Component {
  constructor(props) {
    var state = {color:'#0f0',data:false};
    super(props);
    this.state = state;
  }

  changeHandler(val){
    this.state.color = val;
    color = val;
  }


  render() {
    const colors = [
      '#f00','#0f0','#00f','#000000','salmon','fuchsia','forestgreen','mediumvioletred','hotpink','darkgoldenrod'
    ];

    let items = [];
    for(let i in colors){
      let val = colors[i];
      console.log(val);
      items.push(<div type="button" key={val} style={{'backgroundColor':val}} onClick={()=>{this.changeHandler(val)}} className="btn">{val}</div>)
    }

    return (
      <div className="">
        {items}
      </div>

    );
  }
}


ReactDOM.render(
  React.createElement(App),
  document.getElementById('root')
);


