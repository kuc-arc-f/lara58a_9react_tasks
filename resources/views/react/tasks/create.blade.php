
@extends('layouts.app_react')

@section('title', '新規作成')

@section('content')
<div id="app"></div>

<!-- -->
<script type="text/babel">

class Create extends React.Component {
    constructor(props){
        super(props)
        this.state = {title: '', description: ''}
        this.handleClick = this.handleClick.bind(this);
    }
    componentDidMount(){
    }    
    handleChangeTitle(e){
        this.setState({title: e.target.value})
    }
    handleChangeDesc(e){
        this.setState({description: e.target.value})
    }
    handleClick(){
            console.log("#-handleClick")
            this.add_item()
    //        console.log( this.state )
    }  
    async add_item(){
        var task = {
            title: this.state.title,
            content: this.state.description,
        }
        axios.post('/api/apitasks/create_task' , task ).then(res => {
                console.log(res.data );
                window.location.href = "/react_tasks"
        });
    }
    render() {
        return (
        <div>
            <h1>Task - Create</h1>
            <hr />
            <div className="row">
                <div className="col-md-6">
                    <div className="form-group">
                        <label>Title:</label>
                        <input type="text" className="form-control"
                        onChange={this.handleChangeTitle.bind(this)}/>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-6">
                    <div className="form-group">
                    <label>Content:</label>
                    <input type="text" className="form-control" onChange={this.handleChangeDesc.bind(this)}/>
                    </div>
                </div>
            </div><br />            
            <div className="form-group">
                <button className="btn btn-primary" onClick={this.handleClick}>
                    Create</button>
            </div>            

        </div>
        )
    }
}
ReactDOM.render(<Create />, document.getElementById('app'));
</script>

@endsection
