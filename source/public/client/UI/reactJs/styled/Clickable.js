
import { css } from 'styled-components'

const Clickable = css`
    cursor: pointer;
    &:hover {
        text-shadow: white 0 0 10px, white 0 0 3px;
        opacity: 2;
        color: #deebff;
    }
`;

export {Clickable};