import React from 'react';
import './APStatusBadge.scss';
 
export default React.memo((props) => {

    const statusCategory = props?.status?.category;
    const statusName = props?.status?.name;

    /**
     * The default badge
     */
    var badge = <span
        className="badge badge-green"
    >
        {statusName}
    </span>;

    if (statusCategory === 'in progress') {
        badge = <span
            className="badge badge-yellow"
        >
            {statusName}
        </span>;
    } else if (statusCategory === 'open') {
        badge = <span
            className="badge badge-blue"
        >
            {statusName}
        </span>;
    }

    return badge;
});
