import * as React from "react";

class Arrow extends React.Component {
  render() {
    const { size = "90%" } = this.props;

    return (
      <svg
        id="arrow"
        xmlns="http://www.w3.org/2000/svg"
        width={size}
        height={size}
        viewBox="0 0 32.75 32.75"
      >
        <path
          id="svg-path"
          d="M30.434,15.938c-0.791,0.933-1.917,1.411-3.052,1.411c-0.913,0-1.834-0.312-2.587-0.951l-4.421-3.753V28.75 c0,2.209-1.791,4-4,4c-2.209,0-4-1.791-4-4V12.646l-4.42,3.754c-1.683,1.431-4.208,1.226-5.639-0.459 c-1.43-1.684-1.224-4.208,0.46-5.638l11.01-9.351c1.493-1.27,3.686-1.27,5.178,0l11.011,9.351 C31.658,11.73,31.863,14.255,30.434,15.938z"
        />
      </svg>
    );
  }
}

export default Arrow;
