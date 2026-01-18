import React, {
    useEffect,
    useState
} from 'react';

import {
    Dialog,
    useMediaQuery,
    useTheme,
    FormGroup,
    TableContainer,
    TableCell,
    TableBody,
    Container,
    TableRow,
    DialogContent,
    Radio,
    Table,
    Typography,
    Button,
} from '@material-ui/core';

import './Container.scss';

import { getValueFromLanguage } from './../../../../APHelpers';

import {
    TableHeader,
    DialogFooter,
    DialogHeader,
    TableRowDetailsItem,
} from './../LegalCaseLitigationDetailsPickerForm';
import { getActiveLanguageId } from '../../../../i18n';

export default React.memo((props) => {
    const themeObj = useTheme();
    const fullScreen = useMediaQuery(themeObj.breakpoints.down('md'));

    let data = props?.litigationCaseWithStages ?? null;
    // delete data?.['stages'];

    const [litigationCase, setLitigationCase] = useState(data);
    const [stages, setStages] = useState(props?.litigationCaseWithStages?.stages ?? []);
    const [selectedStageId, setSelectedStageId] = useState('');

    useEffect(() => {
        let data = props?.litigationCaseWithStages;
        // delete data?.['stages'];

        setLitigationCase(data);

        setStages(props?.litigationCaseWithStages?.stages);
    }, [props?.litigationCaseWithStages]);

    const handleStageChange = (e, stage) => {
        setSelectedStageId(stage?.id);

        props.handleStageChange(e, stage);
    }

    let legalCaseClient = litigationCase?.client ?? null;
    let client = '';

    if (legalCaseClient?.company) {
        client = legalCaseClient.company?.name;
    } else if (legalCaseClient?.contact) {
        client = legalCaseClient.contact?.firstName + " " + legalCaseClient.contact?.lastName;
    }

    let tBodyRows = stages?.map((item, key) => {
        let stageName = getValueFromLanguage(item?.stage_name, 'stage_name_languages', getActiveLanguageId(), '');

        let status = getValueFromLanguage(item?.stage_status, 'stage_status_languages', getActiveLanguageId(), '');

        let clientPosition = getValueFromLanguage(item?.stage_client_position, 'client_position_languages', getActiveLanguageId(), '');

        let courtType = item?.stage_court_type?.name ?? '';

        let courtDegree = item?.stage_court_degree?.name ?? '';

        let courtRegion = item?.stage_court_region?.name ?? '';

        let court = item?.stage_court?.name ?? '';

        let externalReferences = [];

        if (item?.stage_external_references) {
            for (var i = 0; i < item.stage_external_references.length; i++) {
                let reference = item.stage_external_references[i];

                externalReferences.push(reference?.number);
            }
        }

        return (
            <TableRow
                key={"legal-case-litigation-detail-history-" + key}
            >
                <TableCell>
                    <FormGroup
                        className="form-group"
                    >
                        <Radio
                            checked={props?.stageId == item.id}
                            onChange={(e) => handleStageChange(e, item)}
                            color="primary"
                            value={selectedStageId}
                            name="case-litigation-detail-history"
                        />
                    </FormGroup>
                </TableCell>
                <TableCell>
                    <Button
                        variant="text"
                        onClick={(e) => handleStageChange(e, item)}
                    >
                        {stageName}
                    </Button>
                </TableCell>
                <TableCell>
                    <Container
                        className="details-container"
                    >
                        <TableRowDetailsItem
                            label="Status"
                            value={status}
                        />
                        <TableRowDetailsItem
                            label="Client"
                            value={client}
                        />
                        <TableRowDetailsItem
                            label="Client Position"
                            value={clientPosition}
                        />
                        <TableRowDetailsItem
                            label="Court Type"
                            value={courtType}
                        />
                        <TableRowDetailsItem
                            label="Court Degree"
                            value={courtDegree}
                        />
                        <TableRowDetailsItem
                            label="Court Region"
                            value={courtRegion}
                        />
                        <TableRowDetailsItem
                            label="Court"
                            value={court}
                        />
                        <TableRowDetailsItem
                            label="External/Court Reference"
                            value={externalReferences?.join()}
                        />
                    </Container>
                </TableCell>
            </TableRow>
        );
    });

    return (
        <Dialog
            open={props.modalState}
            onClose={() => props.setModalState(false)}
            aria-labelledby="form-dialog-title"
            fullScreen={fullScreen}
            closeAfterTransition
            fullWidth={true}
            maxWidth={"md"}
            disableBackdropClick
            className="legal-case-litigation-details-picker-form"
        >
            <DialogHeader
                setModalState={props.setModalState}
            />
            <DialogContent>
                <Container
                    maxWidth={false}
                    className="form-container"
                >
                    <TableContainer>
                        <Table>
                            <TableHeader />
                            <TableBody>
                                {tBodyRows}
                            </TableBody>
                        </Table>
                    </TableContainer>
                </Container>
            </DialogContent>
            <DialogFooter
                setModalState={props.setModalState}
            />
        </Dialog>
    );
});
