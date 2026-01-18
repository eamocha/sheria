import React, {
    useEffect,
    useState
} from 'react';
import './APPagination.scss';
import { makeStyles } from "@material-ui/core";
import { Pagination } from '@material-ui/lab';

const useStyles = makeStyles((theme) => ({
    root: {
        '& > *': {
            marginTop: theme.spacing(2),
        },
    },
}));

export default React.memo((props) => {

    const maxItemsPerPage = 5;

    const [count, setCount] = useState(0);

    var itemsOnPages = Math.floor(props?.numberOfItems / maxItemsPerPage);

    useEffect(() => {

        updateCount();
    }, [props?.numberOfItems]);

    const updateCount = () => {
        if (props.numberOfItems <= maxItemsPerPage) {
            setCount(1);
        } else if (props.numberOfItems % maxItemsPerPage == 0) {
            setCount(itemsOnPages);
        } else {
            setCount(itemsOnPages + 1);
        }
    };

    const classes = useStyles();

    if (count <= 1) {
        return null;
    }

    return (
        <div
            className={classes.root}
        >
            <Pagination
                count={count}
                color="primary"
            />
        </div>
    );
});

