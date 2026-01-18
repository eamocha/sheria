import React, {
    useEffect,
    useState
} from 'react';
import './APPrioritySign.scss';
import low from './../../../assets/images/low.png';
import medium from './../../../assets/images/medium.png';
import high from './../../../assets/images/high.png';
import critical from './../../../assets/images/critical.png';

export default React.memo((props) => {

    const priority = props?.priority ?? 'medium';
    const priorityText = props?.priorityText ?? priority ?? '';
    
    const [priorityImgSrc, setPriorityImgSrc] = useState('');
    const [priorityImgAlt, setPriorityImgAlt] = useState('');

    useEffect(() => {

        loadPriorityImg();
    }, [props?.priority]);

    const loadPriorityImg = () => {
        switch (priority) {
            case 'low':
                setPriorityImgSrc(low);
                setPriorityImgAlt("low priority");
                break;

            case 'high':
                setPriorityImgSrc(high);
                setPriorityImgAlt("high priority");
                break;

            case 'critical':
                setPriorityImgSrc(critical);
                setPriorityImgAlt("critical priority");
                break;
        
            default:
                setPriorityImgSrc(medium);
                setPriorityImgAlt("medium priority");
                break;
        }
    }

    return (
        <span
            className="priority"
        >
            <img
                src={priorityImgSrc}
                alt={priorityImgAlt}
            />
            {priorityText}
        </span>
    );
});
