import React from 'react';
import ReactDOM from 'react-dom';
import StageExternalReferencesTable from './StageExternalReferencesTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<StageExternalReferencesTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});