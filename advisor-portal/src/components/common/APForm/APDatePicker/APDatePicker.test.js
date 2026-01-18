import React from 'react';
import ReactDOM from 'react-dom';
import APDatePicker from './APDatePicker';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APDatePicker />, div);
  ReactDOM.unmountComponentAtNode(div);
});