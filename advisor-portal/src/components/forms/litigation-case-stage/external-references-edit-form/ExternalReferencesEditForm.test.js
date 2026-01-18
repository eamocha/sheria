import React from 'react';
import ReactDOM from 'react-dom';
import ExternalReferencesEditForm from './ExternalReferencesEditForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ExternalReferencesEditForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});