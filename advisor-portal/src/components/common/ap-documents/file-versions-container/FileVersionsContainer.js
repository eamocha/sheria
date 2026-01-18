import React, { useEffect } from 'react';

import './FileVersionsContainer.scss';

import {
    APCollapseContainer
} from './../../ap-collapse/APCollapse';

import {
    Container,
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableRow
} from '@material-ui/core';

import { FileVersionsRow } from '../APDocuments';
 
export default React.memo((props) => {
    useEffect(() => {

    }, [props?.data, props?.file]);

    if (props?.data?.length <= 0) {
        return null;
    }

    let fileName = props?.file?.name ?? '';
    let fileExtension = props?.file?.extension ?? '';

    let tableBodyFirstRow = (
        <FileVersionsRow
            file={props?.file}
            fileName={"Version " + (props?.file?.version ?? "") + " (Current Version)"}
            downloadFile={props.downloadFile}
            deleteFile={props.deleteFile}
            IslatestVersion={true}
        />
    );

    let tableBodyOtherRows = props?.data ? props.data?.map((item, key) => {
        return (
            <FileVersionsRow
                key={"ap-documents-file-versions-row-" + key}
                file={item}
                fileName={"Version " + item?.version}
                downloadFile={props.downloadFile}
                deleteFile={props.deleteFile}
            />
        );
    }) : '';

    return (
        <Container
            maxWidth={false}
            className="ap-documents-file-versions-container"
        >
            <APCollapseContainer
                title={fileName + "." + fileExtension + " versions history"}
                expanded={1}
            >
                <Table>
                    <TableHead>
                        <TableRow>
                            <TableCell>
                                Versions
                            </TableCell>
                            <TableCell>
                                Added By
                            </TableCell>
                            <TableCell>
                                Added On
                            </TableCell>
                            <TableCell />
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {tableBodyFirstRow}
                        {tableBodyOtherRows}
                    </TableBody>
                </Table>
            </APCollapseContainer>
        </Container>
    );
});
