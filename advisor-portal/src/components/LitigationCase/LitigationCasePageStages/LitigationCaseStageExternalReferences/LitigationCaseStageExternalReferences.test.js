import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseStageExternalReferences from './LitigationCaseStageExternalReferences';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseStageExternalReferences />, div);
  ReactDOM.unmountComponentAtNode(div);
});