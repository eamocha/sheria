import React from 'react';
import ReactDOM from 'react-dom';
import ExternalReferencesForm from './ExternalReferencesForm';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ExternalReferencesForm />, div);
  ReactDOM.unmountComponentAtNode(div);
});