import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseEditForm from './LitigationCaseEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});