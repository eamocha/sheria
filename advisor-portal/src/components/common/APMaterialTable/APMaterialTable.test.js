import React from 'react';
import ReactDOM from 'react-dom';
import APMaterialTable from './APMaterialTable';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APMaterialTable />, div);
  ReactDOM.unmountComponentAtNode(div);
});