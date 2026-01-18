import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCaseCustomFieldsEditForm from './LitigationCaseCustomFieldsEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCaseCustomFieldsEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});