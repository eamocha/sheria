import React from 'react';
import ReactDOM from 'react-dom';
import StageExternalReferencesTableRow from './StageExternalReferencesTableRow';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageExternalReferencesTableRow />, div);
  ReactDOM.unmountComponentAtNode(div);
});