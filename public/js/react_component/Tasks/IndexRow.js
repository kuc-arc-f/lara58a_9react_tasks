class IndexRow extends React.Component {
    componentDidMount(){
//        console.log(this.props.obj)
    }
    render(){
        return (
        <tr>
            <td>
                {this.props.obj.id}
            </td>
            <td>
                <a href={"/react_tasks/"+ this.props.obj.id}>{this.props.obj.title}
                </a>
                <a href={"/react_tasks/"+ this.props.obj.id+"/edit"} > [ edit ]
                </a>
            </td>
            <td>
            </td>
        </tr>
        )
    }
}

