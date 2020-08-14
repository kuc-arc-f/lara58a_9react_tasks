@extends('layouts.app_react')
@section('title', 'タスク一覧')

@section('content')
{{ link_to_route('react_tasks.create', 'Create' ,null, ['class' => 'btn btn-primary']) }}
<hr />
<div id="app"></div>

<!-- -->
<script type="text/babel" src="/js/react_component/Tasks/IndexRow.js" ></script>

<script type="text/babel">
class List extends React.Component {
    constructor(props) {
        super(props);
        this.state = {data: ''}
    }
    componentDidMount(){
        this.get_items();
    }
    get_items(){
        axios.get("/api/apitasks/get_tasks").then(res =>  {
            var items = res.data
            var arr =[];
// console.log(items );
            this.setState({ data: items })
        })
    }    
    tabRow(){
        if(this.state.data instanceof Array){
            return this.state.data.map(function(object, index){
                return <IndexRow obj={object} key={index} />
            })
        }
    }
    render(){
        return (
        <div>
            <h1>index</h1>
            <table className="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                {this.tabRow()}
                </tbody>
            </table>            
        </div>
        )
    }
  
}

ReactDOM.render(<List />, document.getElementById('app'));
</script>

@endsection
