@extends('layouts.app_react')

@section('title', "編集")

@section('content')

<div id="app"></div>

<!-- -->
<script type="text/babel">
var task_id= {{$task_id}};

class Edit extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            title: '', 
            content: '' ,
        }
        this.id = parseInt(props.id)
        this.handleClick = this.handleClick.bind(this);
        this.handleClickDelete = this.handleClickDelete.bind(this);
//console.log(this.id)
    }
    componentDidMount(){
        this.get_item( this.id )        
    }
    async get_item(id){
        var task = {
                id: id,
            };        
        axios.post('/api/apitasks/get_item' ,task).then(res =>  {
            var dat = res.data
console.log( dat )
            var item = dat
            this.setState({ 
                title: item.title,
                content: item.content,
            });            
//console.log( this.state.data.title )
        })        
    }
    async save_item(){
        var task = {
            id: this.id,
            title: this.state.title,
            content: this.state.content,
        }
        axios.post('/api/apitasks/update_post', task ).then(res => {
//                console.log(res.data );
            window.location.href = "/react_tasks"
        });        
    }
    async delete_item(){
        var task = {
            id: this.id ,
        };        
        axios.post('/api/apitasks/delete_task',task).then(res =>  {
            console.log( res.data )
            window.location.href = "/react_tasks"
        })        
    }      
    handleChangeTitle(e){
        this.setState({title: e.target.value})
    }
    handleChangeDesc(e){
        this.setState({content: e.target.value})
    }
    handleClick(){
            console.log("#-handleClick")
            this.save_item()
    //        console.log( this.state )
    } 
    handleClickDelete(){
        console.log("#-handleClickDelete")
        this.delete_item()
    }   
    render(){
        return (
        <div className="mt-2">
            <h1>Edit</h1>
            <hr />
            <div className="form-group">
                <label>Title :</label>
                <input type="text" className="form-control" onChange={this.handleChangeTitle.bind(this)}
                value={this.state.title}  />
            </div>
            <div className="form-group">
                <label>content</label>
                <textarea className="form-control"  rows="3" value={this.state.content}
                    onChange={this.handleChangeDesc.bind(this)}></textarea>
            </div>   
            <button onClick={this.handleClick}>Save</button>
            <hr />            
            <button onClick={this.handleClickDelete}>Delete</button>
        </div>
        )
    }
}
ReactDOM.render(<Edit id={task_id}  />, document.getElementById('app'));
</script>

@endsection
