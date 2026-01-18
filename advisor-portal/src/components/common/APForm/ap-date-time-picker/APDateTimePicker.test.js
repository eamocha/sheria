import React from 'react';
import ReactDOM from 'react-dom';
import APDateTimePicker from './APDateTimePicker';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APDateTimePicker />, div);
  ReactDOM.unmountComponentAtNode(div);
});