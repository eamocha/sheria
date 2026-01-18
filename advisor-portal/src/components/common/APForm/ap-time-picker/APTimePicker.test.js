import React from 'react';
import ReactDOM from 'react-dom';
import APTimePicker from './APTimePicker';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APTimePicker />, div);
  ReactDOM.unmountComponentAtNode(div);
});