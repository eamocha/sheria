import React from 'react';
import ReactDOM from 'react-dom';
import ExternalReferences from './ExternalReferences';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<ExternalReferences />, div);
  ReactDOM.unmountComponentAtNode(div);
});