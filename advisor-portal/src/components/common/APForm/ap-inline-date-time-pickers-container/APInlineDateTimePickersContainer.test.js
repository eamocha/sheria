import React from 'react';
import ReactDOM from 'react-dom';
import APInlineDateTimePickersContainer from './APInlineDateTimePickersContainer';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<APInlineDateTimePickersContainer />, div);
  ReactDOM.unmountComponentAtNode(div);
});