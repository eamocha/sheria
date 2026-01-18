import React, {
    useEffect,
    useState
} from 'react';

import './AttachmentItemFile.scss';

import {
    CardMedia,
    CardActionArea
} from '@material-ui/core';

import {
    FileIcon,
    defaultStyles
} from 'react-file-icon';

import Document from '../../../../api/Document';
 
export default React.memo((props) => {
    const [attachmentURL, setAttachmentURL] = useState('');
    const [attachmentBlob, setAttachmentBlob] = useState('');
    const [attachmentName, setAttachmentName] = useState('');
    const [attachmentExtension, setAttachmentExtension] = useState('');
    const [attachmentNameWithExtension, setAttachmentNameWithExtension] = useState('');
    const [attachmentMimeType, setAttachmentMimeType] = useState('');
    const [attachmentLoaded, setAttachmentLoaded] = useState(false);
    const [attachmentIsImage, setAttachmentIsImage] = useState(false);

    useEffect(() => {
        loadAttachment();
        
    }, [props.attachment]);

    const loadAttachment = () => {
        setAttachmentLoaded(false);

        Document.download(props?.attachment?.id).then(response => {
            return response.data;
        }).then(blob => {
            var objectURL = window.URL.createObjectURL(blob);
            var url = objectURL;

            var mime = require('mime-types');

            setAttachmentURL(url);
            setAttachmentBlob(blob);
            setAttachmentName(props?.attachment?.name);
            setAttachmentExtension(props?.attachment?.extension ? props.attachment?.extension.toLowerCase() : '');
            setAttachmentNameWithExtension(props?.attachment?.name + "." + props?.attachment?.extension);
            setAttachmentMimeType(mime.lookup(props.attachment?.extension));

            let extension = props?.attachment?.extension;
            let images = ["jpeg", "jpg", "png", "gif", "bmp"];

            setAttachmentIsImage(images.includes(extension.toLowerCase()));
        }).catch((errors) => {
            console.log(errors);
        }).finally(() => {

            setAttachmentLoaded(true);
        });
    }

    const downloadAttachment = (event) => {
        var FileSaver = require('file-saver');
       
        var blob = new Blob([attachmentBlob], {
            type: attachmentMimeType + ";charset=utf-8"
        });

        FileSaver.saveAs(blob, attachmentNameWithExtension);
    }

    return (
        <CardActionArea
            key={"advisor-task-attachment-card-action-area-" + props?.index}
            onClick={(e) => downloadAttachment(e)}
        >
            {
                attachmentIsImage ?
                <CardMedia
                    className="attachment-image-container"
                    component="img"
                    alt={attachmentName}
                    height="140"
                    image={attachmentURL}
                    title="Download"
                    // So the Blob can be Garbage Collected
                    onLoad={(e) => URL.revokeObjectURL(attachmentURL)}
                    onClick={(e) => e.target.download}
                />
                :
                <div
                    className="attachment-file-container"
                    title="Download"
                    alt={attachmentName}
                >
                    <FileIcon
                        extension={attachmentExtension}
                        {
                            ...defaultStyles[attachmentExtension]
                        }
                    />
                </div>
            }
        </CardActionArea>
    );
});
