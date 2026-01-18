import React from 'react';
import ReactDOM from 'react-dom';
import CustomFieldsEdit from './CustomFieldsEdit';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<CustomFieldsEdit />, div);
  ReactDOM.unmountComponentAtNode(div);
});