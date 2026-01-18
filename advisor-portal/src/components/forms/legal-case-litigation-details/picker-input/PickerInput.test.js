import React from 'react';
import ReactDOM from 'react-dom';
import PickerInput from './PickerInput';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<PickerInput />, div);
  ReactDOM.unmountComponentAtNode(div);
});