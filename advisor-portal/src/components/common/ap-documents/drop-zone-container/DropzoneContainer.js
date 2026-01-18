import React, {
    useEffect,
    useMemo
} from 'react';

import { useDropzone } from 'react-dropzone';

import './DropzoneContainer.scss';

const baseStyle = {
    borderWidth: 0,
    borderRadius: 1,
    borderColor: '#eeeeee',
    borderStyle: 'dashed',
    position: 'absolute',
    minHeight: '80%',
    width: '100%'
};

const activeStyle = {
    borderColor: '#2196f3',
    borderWidth: 2
};

const acceptStyle = {
    borderColor: '#00e676'
};

const rejectStyle = {
    borderColor: '#ff1744'
};

export default React.memo((props) => {

    const {
        acceptedFiles,
        getRootProps,
        getInputProps,
        isDragActive,
        isDragAccept,
        isDragReject } = useDropzone({ noClick: true });

    const style = useMemo(() => ({
        ...baseStyle,
        ...(isDragActive ? activeStyle : {}),
        ...(isDragAccept ? acceptStyle : {}),
        ...(isDragReject ? rejectStyle : {})
    }), [
        isDragActive,
        isDragReject,
        isDragAccept
    ]);

    useEffect(() => {
        if (acceptedFiles[0]) {
            props?.uploadFile(acceptedFiles[0]);
        }
    }, [acceptedFiles]);

    return (
        <div
            {...getRootProps({ style })}
        >
            <input
                {...getInputProps()}
            />
        </div>
    );
});
