import React from 'react';
import ReactDOM from 'react-dom';
import LegalCaseStageStatus from './LegalCaseStageStatus';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LegalCaseStageStatus />, div);
  ReactDOM.unmountComponentAtNode(div);
});