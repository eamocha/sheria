import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageEditForm from './LitigationCaseStageEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});