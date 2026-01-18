import React from 'react';
import ReactDOM from 'react-dom';
import CustomFields from './CustomFields';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<CustomFields />, div);
  ReactDOM.unmountComponentAtNode(div);
});