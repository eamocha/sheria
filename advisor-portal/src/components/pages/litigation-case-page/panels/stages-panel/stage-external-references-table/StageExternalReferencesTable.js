import React from 'react';
import './StageExternalReferencesTable.scss';
import {
    Container,
    Paper,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Typography
} from '@material-ui/core';
import { StageExternalReferencesTableRow } from '../LitigationCasePageStagesPanel';
 
export default React.memo((props) => {
    if (props?.stage?.stage_external_references?.length <= 0) {
        return null;
    }

    let externalReferencesTableRows = props?.stage?.stage_external_references.map((externalReference, key) => {

        return <StageExternalReferencesTableRow
            key={"litigation-case-stage-external-references-row-" + key}
            externalReference={externalReference}
        />
    });

    return (
        <Container
            maxWidth={false}
            className="litigation-case-stage-external-references-table"
        >
            <Typography
                variant="body1"
                className="table-caption"
            >
                External/Court Reference Details
            </Typography>
            <TableContainer
                component={Paper}
            >
                <Table
                    size="small"
                >
                    <TableHead>
                        <TableRow>
                            <TableCell
                                variant="head" 
                                size="medium" 
                                width="50%"
                            >
                                External/Court Reference
                            </TableCell>
                            <TableCell
                                variant="head" 
                                size="small" 
                                width="25%"
                            >
                                External/Court Reference Date
                            </TableCell>
                            <TableCell
                                variant="head" 
                                size="small" 
                                width="25%"
                            >
                                Comments
                            </TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {externalReferencesTableRows}
                    </TableBody>
                </Table>
            </TableContainer>
        </Container>
    );
});
