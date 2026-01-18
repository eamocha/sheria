import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageExternalReferenceAddForm from './LitigationCaseStageExternalReferenceAddForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageExternalReferenceAddForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});