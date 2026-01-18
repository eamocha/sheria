import React, { useContext } from 'react';

import './AttachmentItemData.scss';

import {
    CardContent,
    IconButton,
    Typography
} from '@material-ui/core';

import DeleteIcon from '@material-ui/icons/Delete';

import Document from '../../../../api/Document';

import {
    Context,
    initialGlobalState
} from '../../../../Store';

import {
    addEllipsis,
    isFunction,
    buildErrorMessages
} from '../../../../APHelpers';
 
export default React.memo((props) => {
    const [globalState, globalStateDispatcher] = useContext(Context);

    const deleteAttachment = () => {
        if (window.confirm("Are you sure you want to delete this attachment?")) {
            globalStateDispatcher({
                globalLoader: {
                    ...globalState?.globalLoader,
                    open: true
                }
            });

            Document.delete(props?.attachment?.id).then((response) => {
                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        severity: "success",
                        text: "Task Attachment has been deleted successfully!"
                    }
                });
    
                if (isFunction(props?.loadAdvisorTaskData)) {
                    return props.loadAdvisorTaskData();
                }
            }).catch((error) => {
                let message = buildErrorMessages(error?.response?.data?.message);

                globalStateDispatcher({
                    notificationBar: {
                        ...globalState?.notificationBar,
                        open: true,
                        text: message,
                        severity: "error"
                    },
                    globalLoader: initialGlobalState?.globalLoader
                });
            }).finally(() => {
                /**
                 * so if the reload function is not set we have to hide the global loader,
                 * otherwise, the reload function will hide the global loader itself
                 */
                if (!isFunction(props?.loadAdvisorTaskData)) {
                    globalStateDispatcher({
                        globalLoader: initialGlobalState?.globalLoader
                    });
                }
            });
        }
    };

    if (!props?.attachment) {
        return null;
    }

    return (
        <CardContent
            className="attachment-footer-container"
        >
            <Typography
                variant="body1"
                component="caption"
                className="attachment-name"
                title={props?.attachment?.name}
            >
                {addEllipsis(props?.attachment?.name, 25)}
            </Typography>
            <div
                className="text-right"
            >
                <IconButton
                    size="small"
                    color="secondary"
                    title="Delete Attachment"
                    onClick={() => deleteAttachment()}
                >
                    <DeleteIcon />
                </IconButton>
            </div>
        </CardContent>
    );
});
